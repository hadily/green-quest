import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';


@Component({
  selector: 'app-delete-partner',
  templateUrl: './delete-partner.component.html',
  styleUrl: './delete-partner.component.scss'
})
export class DeletePartnerComponent {
  constructor(
    public dialogRef: MatDialogRef<DeletePartnerComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { id: number },
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
  }

  onDelete(): void {
    this.apiService.deletePartner(this.data.id).subscribe(
      response => {
        console.log('Partner deleted:', response);
        this.dialogRef.close(true); 
        this.refreshService.triggerRefresh('/users/partners'); // Navigate to the partner list page

      },
      error => {
        console.error('Error deleting partner:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); 
  }
}
