import { Component, OnInit } from '@angular/core';
import { switchMap, map, of, Subscription } from 'rxjs';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-account-details',
  templateUrl: './account-details.component.html',
  styleUrl: './account-details.component.scss'
})
export class AccountDetailsComponent implements OnInit{
  user: any;
  fileUrl = environment.fileUrl;
  private refreshSubscription: Subscription;

  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
    this.loadUser();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadUser();
    });

  }

  loadUser(): void {
    const userId = this.authService.currentUserValue?.id ?? 1;
    console.log(userId);
    this.apiService.getUserById(userId).pipe(
      switchMap(user => {
        // Check if the user data was successfully retrieved
        if (!user) {
          throw new Error('User not found');
        }  
        // Based on the role, fetch additional data
        if (user.roles.includes('PARTNER')) {
          return this.apiService.getPartnerById(userId).pipe(
            map((partnerData: any) => ({ ...user, ...partnerData })) // Merge Partner data with user
          );
        }
        this.user = user; // Store the basic user data
        return of(user);
      })
    ).subscribe(
      (      data: {}) => {
        this.user = data; // Store the final merged user data
      },
      (      error: any) => {
        console.error("Error fetching user data:", error);
      }
    );
  }

}
