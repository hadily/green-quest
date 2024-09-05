import { Component, Inject, Input, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-update-product',
  templateUrl: './update-product.component.html',
  styleUrl: './update-product.component.scss'
})
export class UpdateProductComponent implements OnInit{
  product = {
    name: '',
    description: '',
    price: '',
    owner: 1,
    imageFilename: null
  };
  file: any;  
  fileUrl = environment.fileUrl;

  constructor(
    public dialogRef: MatDialogRef<UpdateProductComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { productId: number },
    private apiService: ApiService,
    private refreshService: RefreshService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.product.owner = this.authService.currentUserValue?.id ?? 1;
    this.loadProductData();
  }

  selectImage(event: any) {
    this.product.imageFilename = event.target.files[0];
  }

  loadProductData(): void {
    this.apiService.getProductById(this.data.productId).subscribe(
      response => {
        console.log('Loaded product data:', response);
        this.product = response;
      },
      error => {
        console.error('Error loading product data:', error);
      }
    );  
  }


  onUpdate(): void {
    this.apiService.updateProduct(this.data.productId, this.product).subscribe(
      response => {
        console.log('After updateProduct function', response);
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/partner/services/products'); 
      },
      error => {
        console.error('Error updating product:', error);
      }
    );  
  }
  

  closeModal(): void {
    this.dialogRef.close(); // Close the modal without any action
  }


}
