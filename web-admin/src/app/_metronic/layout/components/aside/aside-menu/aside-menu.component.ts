import { Component, OnInit } from '@angular/core';
import { environment } from '../../../../../../environments/environment';
import { ApiService } from 'src/app/services/api.service';
import { AuthService } from 'src/app/modules/auth';

@Component({
  selector: 'app-aside-menu',
  templateUrl: './aside-menu.component.html',
  styleUrls: ['./aside-menu.component.scss'],
})
export class AsideMenuComponent implements OnInit {
  appAngularVersion: string = environment.appVersion;
  appPreviewChangelogUrl: string = environment.appPreviewChangelogUrl;
  user: any;
  isPartnerUser: boolean = false;

  constructor(
    private apiService: ApiService,
    private authService: AuthService,
  ) {}

  ngOnInit(): void {
    const userId = this.authService.currentUserValue?.id ?? 1;
    this.checkIfPartner(userId);
  }

  checkIfPartner(userId: number): void {
    console.log(userId);
    this.apiService.getUserById(userId).subscribe(
      user => {
        this.isPartnerUser = this.isPartner(user.roles);
        console.log('Is Partner:', this.isPartnerUser);
      },
      error => {
        console.error('Error fetching user:', error);
      }
    );
  }

  isPartner(roles: string[]): boolean {
    console.log("roles: ", roles);
    return roles.includes('PARTNER');
  }
}

