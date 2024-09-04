import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';


@Component({
  selector: 'app-new-event',
  templateUrl: './new-event.component.html',
  styleUrl: './new-event.component.scss'
})
export class NewEventComponent implements OnInit {
  event = {
    name: '',
    description: '',
    startDate: '',
    endDate: '',
    price: 0,
    category: '',
    nbParticipants: 0,
    organizer: 1,
    imageFilename: null,
  };

  constructor(
    public dialogRef: MatDialogRef<NewEventComponent>,
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService,
  ) {}


  ngOnInit(): void {}

  selectImage(event: any) {
    this.event.imageFilename = event.target.files[0];
  }


  onSubmit(): void {
    this.event.organizer = this.authService.currentUserValue?.id ?? 1;
    
    this.apiService.createEvent(this.event).subscribe(
      response => {
        console.log('Event created:', response);
        this.refreshService.triggerRefresh('/partner/services/events');
        this.closeModal();
      },
      error => {
        console.error('Error creating event:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close();
  }

  

}
