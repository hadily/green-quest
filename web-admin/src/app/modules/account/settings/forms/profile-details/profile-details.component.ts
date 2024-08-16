import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { BehaviorSubject, map, of, Subscription, switchMap } from 'rxjs';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-profile-details',
  templateUrl: './profile-details.component.html',
})
export class ProfileDetailsComponent implements OnInit, OnDestroy {
  isLoading$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);
  isLoading: boolean;
  private unsubscribe: Subscription[] = [];
  user: Partial<{
    firstName: string;
    lastName: string;
    phoneNumber: string;
    email: string;
    location: string;
    companyName: string;
    companyDescription: string;
    roles: string[];
    imageFilename: string | null;
  }> = {};
  file: any;

  constructor(
    private cdr: ChangeDetectorRef,
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService
  ) {
    const loadingSubscr = this.isLoading$
      .asObservable()
      .subscribe((res) => (this.isLoading = res));
    this.unsubscribe.push(loadingSubscr);
  }

  ngOnInit(): void {
    console.log(this.isPartner());
    this.loadUser();
  }

  selectImage(event: any) {
    if (event.target.files.length > 0) {
      const file = event.target.files[0];
      this.user.imageFilename = file;
    }
  }

  saveSettings() {
    const userId = this.authService.currentUserValue?.id ?? 0;
    this.file = this.user.imageFilename;

    this.isLoading$.next(true);
  
    // Check if the user is a Partner
    const updateObservable = this.isPartner()
      ? this.apiService.updatePartner(userId, this.user, this.file)
      : this.apiService.updateUser(userId, this.user, this.file);
  
    // Subscribe to the updateObservable to handle the API response
    updateObservable.subscribe(
      (response) => {
        // Handle successful update
        console.log('User updated successfully:', response);
        this.refreshService.triggerRefresh('/crafted/account/overview');
        this.isLoading$.next(false);
      },
      (error) => {
        // Handle error
        console.error('Error updating user:', error);
        this.isLoading$.next(false);
      }
    );
  }
  

  ngOnDestroy() {
    this.unsubscribe.forEach((sb) => sb.unsubscribe());
  }

  loadUser(): void {
    const userId = this.authService.currentUserValue?.id ?? 0;
  
    this.apiService.getUserById(userId).subscribe(
      user => {
        // Store the basic user data
        console.log('Loaded user data:', user);
        this.user = user;
  
        // Wait for the isPartner check to finish
        if (this.isPartner()) {
          // Fetch additional Partner data
          this.apiService.getPartnerById(userId).subscribe(
            partnerData => {
              console.log('Loaded partner data:', partnerData);
              // Merge the Partner data with the basic user data
              this.user = { ...this.user, ...partnerData };
              console.log('Final merged user data:', this.user);
            },
            error => {
              console.error('Error loading partner data:', error);
            }
          );
        } else {
          console.log('User is not a partner. No additional data needed.');
        }
      },
      error => {
        console.error('Error loading user data:', error);
      }
    );
  }
  

  isPartner(): boolean {
    return this.user?.roles?.includes('PARTNER') || false;
  }
}
