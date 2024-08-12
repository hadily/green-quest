import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';
import { ChangeDetectorRef } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { NewPartnerComponent } from '../new-partner/new-partner.component'; 
import { DeletePartnerComponent } from '../delete-partner/delete-partner.component';
import { RefreshService } from 'src/app/services/refresh.service';
import { Subscription } from 'rxjs';
import { UpdatePartnersComponent } from '../update-partners/update-partners.component';

@Component({
  selector: 'app-view-partners',
  templateUrl: './view-partners.component.html',
  styleUrls: ['./view-partners.component.scss']  // Correct property name
})
export class ViewPartnersComponent implements OnInit {
  partners: any[] = [];
  private refreshSubscription: Subscription;
  searchQuery: string = '';


  constructor(
    public dialog: MatDialog, 
    private apiService: ApiService, 
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService
  ) { }  // Use camelCase for service

  ngOnInit(): void {
    this.loadPartners();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadPartners(); // Reload partners when a new partner is added
    });
  }

  loadPartners() : void {
    this.apiService.getAllPartners().subscribe(
      data => {
        this.partners = data;
        this.cdr.detectChanges();  // Manually trigger change detection
        console.log(this.partners);
      },
      error => {
        console.log("Error fetching data:", error);
      }
    );
  }

  openModal(): void {
    const dialogRef = this.dialog.open(NewPartnerComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log('The modal was closed');
    });
  }

  openDeleteModal(id: number): void {
    const dialogRef = this.dialog.open(DeletePartnerComponent, {
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

  openUpdateModal(partnerId: number): void {
    const dialogRef = this.dialog.open(UpdatePartnersComponent, {
      data: { partnerId: partnerId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/users/partners');
      }
    });
  }

  searchPartners(): void {
    console.log(this.searchQuery);
    if (this.searchQuery.trim()) {
      this.apiService.searchPartners(this.searchQuery).subscribe(data => {
        this.partners = data;
      });
    } else {
      this.loadPartners();  // Reload all partners if search query is empty
    }
  }

  onSearchChange(event: any): void {
    console.log(event);
    this.searchQuery = event.target.value;
    this.searchPartners();
  }


}
