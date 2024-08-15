import { Component, Input, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';

@Component({
  selector: 'app-event-panel',
  templateUrl: './event-panel.component.html',
  styleUrls: ['./event-panel.component.scss']
})
export class EventPanelComponent implements OnInit {
  @Input() event!: any;
  eventDetails: any;
  ownerDetails: any;

  constructor(
    private apiService: ApiService
  ) {}

  ngOnInit(): void {
    console.log('Received event: ', this.event);
    if (this.event && this.event.id) {
      this.loadEventDetails();
    }
  }

  loadEventDetails(): void {
    this.apiService.getEventById(this.event.id).subscribe(
      (data) => {
        this.eventDetails = data;
        console.log('eventDetails ', this.eventDetails);
        if (this.eventDetails && this.eventDetails.owner) {
          this.loadOwnerDetails(this.eventDetails.owner.id);
        }
      },
      (error) => {
        console.error('Error fetching event details', error);
      }
    );
  }

  loadOwnerDetails(ownerId: number): void {
    this.apiService.getPartnerById(ownerId).subscribe(
      (data) => {
        this.ownerDetails = data;
        console.log('ownerDetails ', this.ownerDetails);
      },
      (error) => {
        console.error('Error fetching owner details', error);
      }
    );
  }
}
