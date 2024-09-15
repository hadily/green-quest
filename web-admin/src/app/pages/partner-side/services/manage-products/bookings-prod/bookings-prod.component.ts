import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-bookings-prod',
  templateUrl: './bookings-prod.component.html',
  styleUrl: './bookings-prod.component.scss'
})
export class BookingsProdComponent implements OnInit {
  booking = {
    status: '',
    reservationDate: '',
  };
  bookings: any;
  product: any;

  constructor(
    public dialogRef: MatDialogRef<BookingsProdComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { productId: number },
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService,
  ) {}


  ngOnInit(): void {
    this.loadReservations();
    this.loadProductById();
  }

  loadReservations(): void {
    console.log("productID ",this.data.productId);
    this.apiService.getBookingsByProduct(this.data.productId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.productId);
        console.log('Loaded booking:', response); 
        this.bookings = response;
      },
      error => {
        console.error('Error loading bookings:', error);
      }
    );
  }

  loadProductById(): void {
    this.apiService.getProductById(this.data.productId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.productId);
        console.log('Loaded product:', response); 
        this.product = response;
      },
      error => {
        console.error('Error loading product:', error);
      }
    );
  }

  updateBooking(id: number): void {
    console.log('Updated booking:', this.booking);
    this.apiService.updateBooking(id, this.booking).subscribe(
      response => {
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/partner/services/products');
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
          this.refreshService.triggerRefresh('/partner/services/products');
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
