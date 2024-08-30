import { HttpHeaders } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
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
    organizer: 1
  };
  file: any;

  constructor(
    public dialogRef: MatDialogRef<NewEventComponent>,
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService,
    private router: Router
  ) {}


  ngOnInit(): void {}

  selectImage(event: any) {
    this.file = event.target.files[0].name;
  }


  onSubmit(): void {
    this.event.organizer = this.authService.currentUserValue?.id ?? 1;
    if (this.file) {
      this.apiService.createEvent(this.event, this.file).subscribe(
        response => {
          console.log('Event created:', response);
          this.router.navigate(['/partner/services/events']);
          this.closeModal();
        },
        error => {
          console.error('Error creating event:', error);
        }
      );
    } else {
      console.error('No file selected.');
    }
  }

  closeModal(): void {
    this.dialogRef.close();
  }

  

}
