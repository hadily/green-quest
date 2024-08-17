import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Subscription } from 'rxjs';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { PartnerCreateComplaintComponent } from '../partner-create-complaint/partner-create-complaint.component';

@Component({
  selector: 'app-partner-complaints',
  templateUrl: './partner-complaints.component.html',
  styleUrl: './partner-complaints.component.scss'
})
export class PartnerComplaintsComponent implements OnInit{

  complaints: any[] = [];
  users: any[] = [];
  private refreshSubscription: Subscription;

  constructor(
    public dialog: MatDialog, 
    private apiService: ApiService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
    this.loadComplaints();
  }

  loadComplaints() {
    this.apiService.getAllComplaints().subscribe(
      data => {
        this.complaints = data;
        console.log(this.complaints);
        this.cdr.detectChanges();
        
      },
      error => {
        console.error("Error fetching data:", error);
      }
    );
  }

  openModal(complaintId: number): void {
    const dialogRef = this.dialog.open(PartnerCreateComplaintComponent, {
      data: { complaintId: complaintId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/complaints');
      }
    });
  }


  getBadgeClass(status: string): string {
    switch (status.toLowerCase()) {
      case 'new':
        return 'badge badge-light-primary fs-7 fw-bold';
      case 'pending':
        return 'badge badge-light-danger fs-7 fw-bold';
      case 'resolved':
        return 'badge badge-light-success fs-7 fw-bold';
      default:
        return 'badge badge-light-secondary fs-7 fw-bold'; // default class
    }
  }

  createModal(): void {}


}
