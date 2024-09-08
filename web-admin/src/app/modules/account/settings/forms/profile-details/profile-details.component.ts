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
  user: any;
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
    this.loadUser();
  }

  selectImage(event: any) {
    this.file = event.target.files[0].name;
  }

  saveSettings() {
    const userId = this.authService.currentUserValue?.id ?? 1;
    this.isLoading$.next(true);
    // console.log("user data to update ", this.user);

    const formData = new FormData();

  // Append the file if it exists
  if (this.file) {
    formData.append('imageFilename', this.file);
  }

  // Append user data manually
  formData.append('firstName', this.user.firstName);
  formData.append('lastName', this.user.lastName);
  formData.append('email', this.user.email);
  formData.append('phoneNumber', this.user.phoneNumber);
  formData.append('localisation', this.user.localisation);
  formData.append('companyName', this.user.companyName);
  formData.append('companyDescription', this.user.companyDescription);

    const updateObservable = this.isPartner()
      ? this.apiService.updatePartner(userId, formData)
      : this.apiService.updateUser(userId, this.user, this.file);
  
    updateObservable.subscribe(
      (response) => {
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

    this.apiService.getUserById(userId).pipe(
      switchMap(user => {
        if (!user) {
          throw new Error('User not found');
        }  
        if (this.isPartner()) {
          console.log(user);
          return this.apiService.getPartnerById(userId).pipe(
            map((partnerData: any) => ({ ...user, ...partnerData })) 
          );
        }
        this.user = user; 
        return of(user);
      })
    ).subscribe(
      (      data: {}) => {
        this.user = data; 
      },
      (      error: any) => {
        console.error("Error fetching user data:", error);
      }
    );
  }
  

  isPartner(): boolean {
    return this.user?.roles?.includes('PARTNER') || false;
  }
}
