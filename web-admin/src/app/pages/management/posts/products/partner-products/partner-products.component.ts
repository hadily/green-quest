import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ProductContainerComponent } from '../product-container/product-container.component';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-partner-products',
  standalone: true,
  imports: [
    ProductContainerComponent,
    CommonModule
  ],
  templateUrl: './partner-products.component.html',
  styleUrl: './partner-products.component.scss'
})
export class PartnerProductsComponent implements OnInit{
  products: any[] = []; 
  private refreshSubscription: Subscription;

  constructor(
    private apiService: ApiService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService,
  ) {}

  ngOnInit(): void {
    this.loadProducts();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadProducts(); 
    });
  }

  loadProducts() {
    this.apiService.getProducts().subscribe(
        data => {
            console.log("Fetched products:", data);
            this.products = data;
            this.cdr.detectChanges();
        },
        error => {
            console.error("Error fetching data:", error);
        }
    );
  }

  trackByProductId(index: number, product: any): number {
    return product.id;
  }

  
}
