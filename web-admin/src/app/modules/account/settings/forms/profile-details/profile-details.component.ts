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

  saveSettings() {
    this.isLoading$.next(true);
  
    // Check if the user is a Partner
    const updateObservable = this.isPartner()
      ? this.apiService.updatePartner(this.user.id, this.user)
      : this.apiService.updateUser(this.user.id, this.user);
  
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

    this.apiService.getUserById(userId).pipe(
      switchMap(user => {
        // Check if the user data was successfully retrieved
        if (!user) {
          throw new Error('User not found');
        }
  
        this.user = user; // Store the basic user data
        console.log('Basic user data:', this.user);
  
        // Based on the role, fetch additional data
        if (user.roles.includes('PARTNER')) {
          return this.apiService.getPartnerById(userId).pipe(
            map((partnerData: any) => ({ ...user, ...partnerData })) // Merge Partner data with user
          );
        } else {
          // If the role is not recognized, return the basic user data
          return of(user);
        }
      })
    ).subscribe(
      (      data: {}) => {
        this.user = data; // Store the final merged user data
        console.log('Final user data:', this.user);
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
