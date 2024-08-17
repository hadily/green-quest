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
    serviceName: '',
    description: '',
    startDate: '',
    endDate: '',
    available: '',
    price: '',
    ownerId: 0,
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
    const userId = this.authService.currentUserValue?.id ?? 0;
    console.log(userId);
    this.product.ownerId = userId;
    this.loadProductData();
  }

  selectImage(event: any) {
    this.file = event.target.files[0];
    let reader = new FileReader();
    reader.onload = function () {
      let output: any = document.getElementById('imageFilename');
      output.src = reader.result;
    }
    reader.readAsDataURL(this.file);
  }

  loadProductData(): void {
    console.log(this.data.productId);
    this.apiService.getProductById(this.data.productId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.productId);
        console.log('Loaded partner data:', response); // Debugging log
        this.product = response;
      },
      error => {
        console.error('Error loading partner data:', error);
      }
    );
  }
  

  onUpdate(): void {
    console.log('Product ID:', this.data.productId);
    this.file = this.product.imageFilename;
    console.log('Inside onUpdate');
  
    this.apiService.updateProduct(this.data.productId, this.product, this.file).subscribe(
      response => {
        console.log('After updateProduct function', response);
  
        // Close the modal and trigger a refresh after a successful update
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/partner/services/products'); 
      },
      error => {
        console.error('Error updating product:', error);
        // Optionally show an error message to the user
      }
    );
  }
  

  closeModal(): void {
    this.dialogRef.close(); // Close the modal without any action
  }


}
