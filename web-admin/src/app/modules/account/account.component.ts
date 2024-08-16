import { Component, OnInit } from '@angular/core';
import { ApiService } from "../../services/api.service";
import { ActivatedRoute } from '@angular/router';
import { AuthService } from '../auth';
import { map, of, switchMap } from 'rxjs';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
})

export class AccountComponent implements OnInit {
  
  user: any;
  fileUrl = environment.fileUrl;

  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private route: ActivatedRoute,
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
      },
      (      error: any) => {
        console.error("Error fetching user data:", error);
      }
    );
  }
  

}
