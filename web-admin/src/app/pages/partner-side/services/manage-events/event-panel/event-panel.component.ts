import { Component, Inject, Input, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-event-panel',
  templateUrl: './event-panel.component.html',
  styleUrls: ['./event-panel.component.scss']
})
export class EventPanelComponent implements OnInit {
  event = {
    name: '',
    description: '',
    startDate: '',
    endDate: '',
    category: '',
    price: '',
    owner: 0,
    imageFilename: null,
    nbParticipants: 0
  };
  file: any;  
  fileUrl = environment.fileUrl;

  constructor(
    public dialogRef: MatDialogRef<EventPanelComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { eventId: number },
    private apiService: ApiService,
    private refreshService: RefreshService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loadArticleData();
  }

  selectImage(event: any) {
    this.event.imageFilename = event.target.files[0];
  }

  loadArticleData(): void {
    this.apiService.getEventById(this.data.eventId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.eventId);
        console.log('Loaded article data:', response); // Debugging log
        this.event = response;
      },
      error => {
        console.error('Error loading partner data:', error);
      }
    );
  }

  onUpdate(): void {
    this.event.owner = this.authService.currentUserValue?.id ?? 0;
    this.fileUrl = this.fileUrl.replace(/\/+$/, '');
    this.apiService.updateEvent(this.data.eventId, this.event).subscribe(
      response => {
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/partner/services/events');
        this.closeModal(); 
      },
      error => {
        console.error('Error updating event:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); // Close the modal without any action
  }

  
}
