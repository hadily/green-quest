import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';
import { environment } from 'src/environments/environment';


@Component({
  selector: 'app-event-container',
  standalone: true,
  templateUrl: './event-container.component.html',
  styleUrl: './event-container.component.scss'
})
export class EventContainerComponent implements OnInit {
  @Input() event: any;
  organizer: any;
  fileUrl = environment.fileUrl;


  constructor(
    private apiService: ApiService,
    private cdr: ChangeDetectorRef
  ) {}


  ngOnInit(): void {
    console.log('Event data received in EventContainerComponent:', this.event);
    this.loadPartner();
    this.cdr.detectChanges();
  }

  loadPartner(): void {
    console.log('Owner ID:', this.event.organizer);
    this.apiService.getPartnerById(this.event.organizer).subscribe(
      data => {
        this.organizer = data;
        console.log('Partner data:', this.organizer);
      },
      error => console.error('Error fetching partner:', error)
    );
  }
}
