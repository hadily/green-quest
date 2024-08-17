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
    serviceName: '',
    description: '',
    startDate: '',
    endDate: '',
    available: '',
    price: '',
    ownerId: 0,
    imageFilename: null
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

  ngOnInit(): void {}

  selectImage(event: any) {
    this.file = event.target.files[0];
    let reader = new FileReader();
    reader.onload = function () {
      let output: any = document.getElementById('imageFilename');
      output.src = reader.result;
    }
    reader.readAsDataURL(this.file);
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
    this.event.ownerId = this.authService.currentUserValue?.id ?? 0;
    this.file = this.event.imageFilename;
    this.apiService.updateEvent(this.data.eventId, this.event, this.file).subscribe(
      response => {
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/partner/services/events'); // Notify other components
      },
      error => {
        console.error('Error updating event:', error);
        // Optionally show an error message to the user
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); // Close the modal without any action
  }

  
}
