import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';
import { ChangeDetectorRef } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { RefreshService } from 'src/app/services/refresh.service';
import { Subscription } from 'rxjs';
import { NewAdminComponent } from '../new-admin/new-admin.component';
import { UpdateAdminComponent } from '../update-admin/update-admin.component';
import { DeleteAdminComponent } from '../delete-admin/delete-admin.component';

@Component({
  selector: 'app-view-admins',
  templateUrl: './view-admins.component.html',
  styleUrl: './view-admins.component.scss'
})
export class ViewAdminsComponent implements OnInit {
  admins: any[] = [];
  private refreshSubscription: Subscription;
  searchQuery: string = '';

  constructor(
    private apiService: ApiService, 
    private cdr: ChangeDetectorRef,
    private dialog: MatDialog,
    private refreshService: RefreshService
  ) { }  // Use camelCase for service

  ngOnInit(): void {
    this.loadAdmins();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadAdmins(); // Reload partners when a new partner is added
    });
  }

  loadAdmins(): void {
    this.apiService.getAllAdmins().subscribe(
      data => {
        this.admins = data;
        this.cdr.detectChanges();  // Manually trigger change detection
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
