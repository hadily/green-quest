import { Component, OnInit } from '@angular/core';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';

@Component({
  selector: 'app-new-event',
  templateUrl: './new-event.component.html',
  styleUrl: './new-event.component.scss'
})
export class NewEventComponent implements OnInit {
  event = {
    serviceName: '',
    description: '',
    startDate: '',
    endDate: '',
    price: 0,
    available: true,
    ownerId: 0
  };

  constructor(
    private apiService: ApiService,
    private authService: AuthService
  ) {}


  ngOnInit(): void {
    this.setCurrentUserAsOwner();
  }

  setCurrentUserAsOwner(): void {
    const userId = this.authService.currentUserValue?.id ?? 15;
    this.event.ownerId = userId;
  }

  onSubmit(): void {
    this.apiService.createEvent(this.event).subscribe(response => {
      console.log('Event created:', response);
    });
  }

  

}
