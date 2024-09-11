import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common'; // Import CommonModule
import { EventContainerComponent } from '../event-container/event-container.component';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-partner-events',
  standalone: true,
  imports: [
    EventContainerComponent,
    CommonModule
  ],
  templateUrl: './partner-events.component.html',
  styleUrl: './partner-events.component.scss'
})
export class PartnerEventsComponent implements OnInit{
  events: any[] = []; 
  private refreshSubscription: Subscription;

  constructor(
    private apiService: ApiService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService,
  ) {}

  ngOnInit(): void {
    this.loadEvents();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadEvents(); 
    });
  }

  loadEvents() {
    this.apiService.getEvents().subscribe(
        data => {
            console.log("Fetched events:", data);
            this.events = data;
            this.cdr.detectChanges();
        },
        error => {
            console.error("Error fetching data:", error);
        }
    );
  }

  trackByEventId(index: number, event: any): number {
    return event.id;
  }
  

}
