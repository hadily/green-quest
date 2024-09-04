import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Subscription } from 'rxjs';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { environment } from 'src/environments/environment';
import { UpdateProductComponent } from '../update-product/update-product.component';
import { DeleteProductComponent } from '../delete-product/delete-product.component';
import { NewProductComponent } from '../new-product/new-product.component';
import { AuthService } from 'src/app/modules/auth';

@Component({
  selector: 'app-products',
  templateUrl: './products.component.html',
  styleUrl: './products.component.scss'
})
export class ProductsComponent implements OnInit{
  products: any[] = [];
  private refreshSubscription: Subscription;
  fileUrl = environment.fileUrl;

  constructor(
    public dialog: MatDialog, 
    private apiService: ApiService, 
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService,
    private authService: AuthService
  ) { }  // Use camelCase for service

  ngOnInit(): void {
    this.loadProducts();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadProducts(); // Reload partners when a new partner is added
    });
  }

  loadProducts() : void {
    const userId = this.authService.currentUserValue?.id ?? 1;
    console.log("userID ",userId);
    this.apiService.getAllProductsByOwner(userId).subscribe(
      data => {
        this.products = data;
        console.log(this.products);
        this.cdr.detectChanges();  // Manually trigger change detection
        console.log(this.products);
      },
      error => {
        console.log("Error fetching data:", error);
      }
    );
  }

  openModal(): void {
    const dialogRef = this.dialog.open(NewProductComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log('The modal was closed');
    });
  }

  openDeleteModal(id: number): void {
    const dialogRef = this.dialog.open(DeleteProductComponent, {
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

  openUpdateModal(productId: number): void {
    const dialogRef = this.dialog.open(UpdateProductComponent, {
      data: { productId: productId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/partner/services/products');
      }
    });
  }


}
