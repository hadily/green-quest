import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-delete-client',
  templateUrl: './delete-client.component.html',
  styleUrl: './delete-client.component.scss'
})
export class DeleteClientComponent {
  constructor(
    public dialogRef: MatDialogRef<DeleteClientComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { id: number },
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
  }

  onDelete(): void {
    this.apiService.deleteClient(this.data.id).subscribe(
      response => {
        console.log('Client deleted:', response);
        this.dialogRef.close(true); 
        this.refreshService.triggerRefresh('/users/clients'); // Navigate to the partner list page

      },
      error => {
        console.error('Error deleting client:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); 
  }
}
