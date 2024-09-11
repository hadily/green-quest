import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { environment } from 'src/environments/environment';
import { CommonModule } from '@angular/common';


@Component({
  selector: 'app-event-container',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './event-container.component.html',
  styleUrls: ['./event-container.component.scss']
})
export class EventContainerComponent implements OnInit {
  @Input() event: any;
  organizer: any;
  fileUrl = environment.fileUrl;
  private refreshSubscription: Subscription;



  constructor(
    private apiService: ApiService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService
  ) {}


  ngOnInit(): void {
    console.log('Event data received in EventContainerComponent:', this.event);
    this.loadPartner();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadPartner();
    });
  }

  loadPartner(): void {
    console.log('Owner ID:', this.event.organizer);
    if (this.event && this.event.organizer) {
      this.apiService.getPartnerById(this.event.organizer).subscribe(
        data => {
          this.organizer = data;
          console.log('Partner data:', this.organizer);
          this.cdr.detectChanges();
        },
        error => console.error('Error fetching partner:', error)
      );
    } else {
      console.warn('Organizer ID is missing or undefined');
    }
  }
}
