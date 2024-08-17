import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/services/api.service';
import { AuthService } from 'src/app/modules/auth';

@Component({
  selector: 'app-header-menu',
  templateUrl: './header-menu.component.html',
  styleUrls: ['./header-menu.component.scss'],
})
export class HeaderMenuComponent implements OnInit {
  user: any;
  isPartnerUser: boolean = false;

  constructor(
    private router: Router,
    private apiService: ApiService,
    private authService: AuthService,
  ) {}

  ngOnInit(): void {
    const userId = this.authService.currentUserValue?.id ?? 0;
    this.checkIfPartner(userId);
  }

  checkIfPartner(userId: number): void {
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
    return roles.includes('PARTNER');
  }

  calculateMenuItemCssClass(url: string): string {
    return checkIsActive(this.router.url, url) ? 'active' : '';
  }
}

const getCurrentUrl = (pathname: string): string => {
  return pathname.split(/[?#]/)[0];
};

const checkIsActive = (pathname: string, url: string) => {
  const current = getCurrentUrl(pathname);
  if (!current || !url) {
    return false;
  }

  if (current === url) {
    return true;
  }

  if (current.indexOf(url) > -1) {
    return true;
  }

  return false;
};
