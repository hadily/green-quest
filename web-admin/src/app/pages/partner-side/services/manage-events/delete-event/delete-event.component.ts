import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-delete-event',
  standalone: true,
  imports: [],
  templateUrl: './delete-event.component.html',
  styleUrl: './delete-event.component.scss'
})
export class DeleteEventComponent implements OnInit{

  constructor(
    public dialogRef: MatDialogRef<DeleteEventComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { id: number },
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
  }

  onDelete(): void {
    this.apiService.deleteEvent(this.data.id).subscribe(
      response => {
        console.log('event deleted:', response);
        this.dialogRef.close(true); 
        this.refreshService.triggerRefresh('/partner/services/events'); // Navigate to the partner list page

      },
      error => {
        console.error('Error deleting event:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); 
  }

}
