import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { LayoutService } from '../../core/layout.service';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { Router } from '@angular/router';
import { environment } from 'src/environments/environment';
import { Subscription } from 'rxjs';

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
  user: any;
  fileUrl = environment.fileUrl;
  private refreshSubscription: Subscription;


  constructor(
    private layout: LayoutService,
    private authService: AuthService,
    private apiService: ApiService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService,
  ) {}

  ngOnInit(): void {
    this.headerLeft = this.layout.getProp('header.left') as string;
    this.loadUser();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadUser(); 
    });
  }

  loadUser(): void {
    const userId = this.authService.currentUserValue?.id ?? 1;
    console.log("userId ", userId);
    this.apiService.getUserById(userId).subscribe(
        data => {
            console.log("Fetched user:", data);
            this.user = data;
            this.cdr.detectChanges();
        },
        error => {
            console.error("Error fetching data:", error);
        }
    );
  }

  logout(): void {
    this.authService.logout(); 
  }
}
