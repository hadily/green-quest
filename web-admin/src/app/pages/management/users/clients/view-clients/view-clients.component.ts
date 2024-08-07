import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';
import { ChangeDetectorRef } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { RefreshService } from 'src/app/services/refresh.service';
import { Subscription } from 'rxjs';
import { NewClientComponent } from '../new-client/new-client.component';
import { UpdateClientComponent } from '../update-client/update-client.component';
import { DeleteClientComponent } from '../delete-client/delete-client.component';

@Component({
  selector: 'app-view-clients',
  templateUrl: './view-clients.component.html',
  styleUrl: './view-clients.component.scss'
})
export class ViewClientsComponent implements OnInit{
  clients: any[] = [];
  private refreshSubscription: Subscription;
  searchQuery: string = '';

  constructor(
    private apiService: ApiService, 
    private cdr: ChangeDetectorRef,
    private dialog: MatDialog,
    private refreshService: RefreshService
  ) { }  // Use camelCase for service

  ngOnInit(): void {
    this.loadClients();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadClients(); // Reload partners when a new partner is added
    });
  }

  loadClients(): void {
    this.apiService.getAllClients().subscribe(
      data => {
        this.clients = data;
        this.cdr.detectChanges();  // Manually trigger change detection
        console.log(this.clients);
      },
      error => {
        console.log("Error fetching data:", error);
      }
    );
  }

  searchClients(): void {
    if (this.searchQuery.trim()) {
      this.apiService.searchClients(this.searchQuery).subscribe(data => {
        this.clients = data;
      });
    } else {
      this.loadClients();  // Reload all partners if search query is empty
    }
  }

  onSearchChange(event: any): void {
    this.searchQuery = event.target.value;
    this.searchClients();
  }

  openModal(): void {
    const dialogRef = this.dialog.open(NewClientComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log('The modal was closed');
    });
  }

  openUpdateModal(clientId: number): void {
    console.log(clientId);
    const dialogRef = this.dialog.open(UpdateClientComponent, {
      data: { clientId: clientId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/users/clients');
      }
    });
  }

  openDeleteModal(id: number): void {
    const dialogRef = this.dialog.open(DeleteClientComponent, {
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
