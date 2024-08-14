import { Component, OnInit } from '@angular/core';
import { LayoutService } from '../../core/layout.service';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-topbar',
  templateUrl: './topbar.component.html',
  styleUrls: ['./topbar.component.scss'],
})
export class TopbarComponent implements OnInit {
  toolbarButtonMarginClass = 'ms-1 ms-lg-3';
  toolbarButtonHeightClass = 'w-30px h-30px w-md-40px h-md-40px';
  toolbarUserAvatarHeightClass = 'symbol-30px symbol-md-40px';
  toolbarButtonIconSizeClass = 'svg-icon-1';
  headerLeft: string = 'menu';

  constructor(
    private layout: LayoutService,
    private authService: AuthService,
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
    this.headerLeft = this.layout.getProp('header.left') as string;
  }

  logout(): void {
    this.authService.logout(); // Remove token from localStorage
    this.refreshService.triggerRefresh('/auth/login'); // Navigate to login page
  }
}
