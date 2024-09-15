import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { EventPanelComponent } from '../event-panel/event-panel.component';
import { ApiService } from 'src/app/services/api.service';
import { AuthService } from 'src/app/modules/auth';
import { Subscription } from 'rxjs';
import { RefreshService } from 'src/app/services/refresh.service';
import { MatDialog } from '@angular/material/dialog';
import { DeleteEventComponent } from '../delete-event/delete-event.component';
import { NewEventComponent } from '../new-event/new-event.component';
import { BookingsComponent } from '../bookings/bookings.component';

@Component({
  selector: 'app-events',
  templateUrl: './events.component.html',
  styleUrl: './events.component.scss'
})
export class EventsComponent implements OnInit {
  events: any[] = [];
  private refreshSubscription: Subscription;
  
  constructor(
    public dialog: MatDialog, 
    private apiService: ApiService,
    private authService: AuthService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
    this.loadOwnerEvents();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadOwnerEvents();
    });
  }

  // Load events for the current user
  loadOwnerEvents(): void {
    const userId = this.authService.currentUserValue?.id;
    console.log(userId);
    if (userId) {
      this.apiService.getAllEventsByOwner(userId).subscribe(
        (data: any[]) => {
          this.events = data;
          console.log(this.events);
          this.cdr.detectChanges();
        },
        (error) => {
          console.error('Error fetching owner events', error);
        }
      );
    } else {
      console.warn('User ID is not available');
    }
  }

  openModal(): void {
    const dialogRef = this.dialog.open(NewEventComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log('The modal was closed');
    });
  }

  openDeleteModal(id: number): void {
    const dialogRef = this.dialog.open(DeleteEventComponent, {
      data: { id } 
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        console.log('Deletion confirmed.');
      } else {
        console.log('Deletion canceled.');
      }
    });
  }

  openUpdateModal(eventId: number): void {
    console.log(eventId);
    const dialogRef = this.dialog.open(EventPanelComponent, {
      data: { eventId: eventId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/partner/services/events');
      }
    });
  }

  openBookingModal(eventId: number): void {
    console.log(eventId);
    const dialogRef = this.dialog.open(BookingsComponent, {
      data: { eventId: eventId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/partner/services/events');
      }
    });
  }

}
