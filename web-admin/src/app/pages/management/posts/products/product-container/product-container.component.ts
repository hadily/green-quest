import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-product-container',
  standalone: true,
  templateUrl: './product-container.component.html',
  styleUrl: './product-container.component.scss'
})
export class ProductContainerComponent {
  @Input() product: any;
  owner: any;
  fileUrl = environment.fileUrl;

  constructor(
    private apiService: ApiService,
    private cdr: ChangeDetectorRef
  ) {}


  ngOnInit(): void {
    console.log('product data received in EventContainerComponent:', this.product);
    this.loadPartner();
    this.cdr.detectChanges();
  }

  loadPartner(): void { 
    console.log('Owner ID:', this.product.organizer);
    this.apiService.getPartnerById(this.product.organizer).subscribe(
      data => {
        this.owner = data;
        console.log('Partner data:', this.owner);
      },
      error => console.error('Error fetching partner:', error)
    );
  }

  
}
