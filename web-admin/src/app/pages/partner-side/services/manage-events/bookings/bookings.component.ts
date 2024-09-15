import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-bookings',
  templateUrl: './bookings.component.html',
  styleUrl: './bookings.component.scss'
})
export class BookingsComponent implements OnInit{
  booking = {
    status: '',
    reservationDate: '',
  };
  bookings: any;
  event: any;

  constructor(
    public dialogRef: MatDialogRef<BookingsComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { eventId: number },
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService,
  ) {}


  ngOnInit(): void {
    this.loadReservations();
    this.loadEventById();
  }

  loadReservations(): void {
    this.apiService.getBookingsByEvent(this.data.eventId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.eventId);
        console.log('Loaded booking:', response); 
        this.bookings = response;
      },
      error => {
        console.error('Error loading bookings:', error);
      }
    );
  }

  loadEventById(): void {
    this.apiService.getEventById(this.data.eventId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.eventId);
        console.log('Loaded event:', response); 
        this.event = response;
      },
      error => {
        console.error('Error loading event:', error);
      }
    );
  }

  updateBooking(id: number): void {
    console.log('Updated booking:', this.booking);
    this.apiService.updateBooking(id, this.booking).subscribe(
      response => {
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/partner/services/events');
        this.closeModal();
      },
      error => {
        console.error('Error updating booking:', error);
      }
    );
  }

  onStatusChange(event: Event): void {
    const selectElement = event.target as HTMLSelectElement;
    console.log('Selected status:', selectElement.value); 
    this.booking.status = selectElement.value;
  }

  onDelete(id: number): void {
    const isConfirmed = confirm('Are you sure you want to delete this booking?');
  
    if (isConfirmed) {
      console.log("booking id: ", id);
      this.apiService.deleteBooking(id).subscribe(
        response => {
          console.log('booking deleted:', response);
          this.refreshService.triggerRefresh('/partner/services/events');
          this.closeModal();
        },
        error => {
          console.error('Error deleting booking:', error);
        }
      );
    }
  }

  closeModal(): void {
    this.dialogRef.close(); 
  }


}
