import { Component, OnInit } from '@angular/core';
import { EventPanelComponent } from '../event-panel/event-panel.component';
import { ApiService } from 'src/app/services/api.service';
import { AuthService } from 'src/app/modules/auth';

@Component({
  selector: 'app-events',
  templateUrl: './events.component.html',
  styleUrl: './events.component.scss'
})
export class EventsComponent implements OnInit {
  events: any[] = [];

  constructor(
    private apiService: ApiService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loadEvents();
  }

  loadEvents(): void {
    this.apiService.getEvents().subscribe(
      (data: any[]) => {
        this.events = data;
        console.log("events: ", this.events);
      },
      (error) => {
        console.error('Error fetching events', error);
      }
    );
  }

  getOwnerEvents(): any[] {
    const userId = this.authService.currentUserValue?.id ?? 0;
    return this.events.filter(event => event.ownerId === userId);
  }

}
