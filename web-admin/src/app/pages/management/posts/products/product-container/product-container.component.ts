import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-product-container',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './product-container.component.html',
  styleUrl: './product-container.component.scss'
})
export class ProductContainerComponent {
  @Input() product: any;
  owner: any;
  fileUrl = environment.fileUrl;
  private refreshSubscription: Subscription;


  constructor(
    private apiService: ApiService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService
  ) {}


  ngOnInit(): void {
    console.log('product data received in EventContainerComponent:', this.product);
    this.loadPartner();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadPartner();
    });
  }

  loadPartner(): void { 
    console.log('Owner ID:', this.product.owner);
    if (this.product && this.product.owner) {
      this.apiService.getPartnerById(this.product.owner).subscribe(
        data => {
          this.owner = data;
          console.log('Partner data:', this.owner);
          this.cdr.detectChanges();
        },
        error => console.error('Error fetching partner:', error)
      );
    } else {
      console.warn('Owner ID is missing or undefined');
    }
  }

  
}
