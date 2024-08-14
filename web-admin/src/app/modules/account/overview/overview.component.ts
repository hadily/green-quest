import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';
import { AuthService } from '../../auth';
import { map, of, switchMap } from 'rxjs';

@Component({
  selector: 'app-overview',
  templateUrl: './overview.component.html',
})
export class OverviewComponent implements OnInit {
  user: any;

  constructor(
    private apiService: ApiService,
    private authService: AuthService,
  ) {}

  ngOnInit(): void {
    this.loadUser();
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
