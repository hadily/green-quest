import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-update-complaints',
  templateUrl: './update-complaints.component.html',
  styleUrl: './update-complaints.component.scss'
})
export class UpdateComplaintsComponent {
  complaint = {
    subject: '',
    details: '',
    owner: '',
    relatedTo: '',
    date: '',
    reply: '',
    status:''
  };

  constructor(
    public dialogRef: MatDialogRef<UpdateComplaintsComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { complaintId: number },
      private apiService: ApiService,
      private refreshService: RefreshService
    ) {
      console.log('Dialog data:', data);
    }

  ngOnInit(): void {
      this.loadComplaintData();
  }

  loadComplaintData(): void {
    this.apiService.getComplaintById(this.data.complaintId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.complaintId);
        console.log('Loaded article data:', response); // Debugging log
        this.complaint = response;
      },
      error => {
        console.error('Error loading partner data:', error);
      }
    );
  }

  onUpdate(): void {
    this.apiService.updateComplaints(this.data.complaintId, this.complaint).subscribe(
      response => {
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/complaints'); // Notify other components
      },
      error => {
        console.error('Error updating complaint:', error);
        // Optionally show an error message to the user
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); // Close the modal without any action
  }

}
