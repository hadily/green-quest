import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';
import { ChangeDetectorRef } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { RefreshService } from 'src/app/services/refresh.service';
import { Subscription } from 'rxjs';
import { NewAdminComponent } from '../new-admin/new-admin.component';
import { UpdateAdminComponent } from '../update-admin/update-admin.component';
import { DeleteAdminComponent } from '../delete-admin/delete-admin.component';
import { environment } from 'src/environments/environment';
import { AuthService } from 'src/app/modules/auth/services/auth.service';

@Component({
  selector: 'app-view-admins',
  templateUrl: './view-admins.component.html',
  styleUrl: './view-admins.component.scss'
})
export class ViewAdminsComponent implements OnInit {
  admins: any[] = [];
  private refreshSubscription: Subscription;
  searchQuery: string = '';
  fileUrl = environment.fileUrl;
  currentUserId: number | undefined;

  constructor(
    private apiService: ApiService, 
    private cdr: ChangeDetectorRef,
    private dialog: MatDialog,
    private refreshService: RefreshService,
    private authService: AuthService
  ) { }  

  ngOnInit(): void {
    this.currentUserId = this.authService.currentUserValue?.id;
    this.loadAdmins();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadAdmins(); 
    });
  }

  loadAdmins(): void {
    this.apiService.getAllAdmins().subscribe(
      data => {
        this.admins = data.filter((admin: { id: number | undefined; }) => admin.id !== this.currentUserId);
        this.cdr.detectChanges();  
        console.log(this.admins);
      },
      error => {
        console.log("Error fetching data:", error);
      }
    );
  }

  searchClients(): void {
    if (this.searchQuery.trim()) {
      this.apiService.searchClients(this.searchQuery).subscribe(data => {
        this.admins = data;
      });
    } else {
      this.loadAdmins();  // Reload all partners if search query is empty
    }
  }

  onSearchChange(event: any): void {
    this.searchQuery = event.target.value;
    this.searchClients();
  }

  openModal(): void {
    const dialogRef = this.dialog.open(NewAdminComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log('The modal was closed');
    });
  }

  openUpdateModal(adminId: number): void {
    console.log(adminId);
    const dialogRef = this.dialog.open(UpdateAdminComponent, {
      data: { adminId: adminId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/users/admins');
      }
    });
  }

  openDeleteModal(id: number): void {
    const dialogRef = this.dialog.open(DeleteAdminComponent, {
      data: { id } 
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        console.log('Deletion confirmed.');
      } else {
        console.log('Deletion canceled.');
      }
    });
  }

}
