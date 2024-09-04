import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-new-product',
  templateUrl: './new-product.component.html',
  styleUrl: './new-product.component.scss'
})
export class NewProductComponent implements OnInit{
  product = {
    name: '',
    description: '',
    price: 0,
    owner: 1,
  };
  file: any;

  constructor(
    public dialogRef: MatDialogRef<NewProductComponent>,
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService,
    private router: Router
  ) {}

  ngOnInit(): void {}

  selectImage(event: any) {
    const file = event.target.files[0]; // Access the first selected file
    if (file) {
      this.file = file.name; // Store the file name (you may want to store the file itself if needed)
    }
  }

  onSubmit(): void {
    this.product.owner = this.authService.currentUserValue?.id ?? 0;
    this.apiService.createProduct(this.product, this.file).subscribe(response => {
      console.log("product: ", this.product);
      console.log('Event created:', response);
      this.router.navigate(['/partner/services/products']); 
      this.closeModal();
    },
    error => {
      console.error('Error creating article:', error);
    });
  }

  closeModal(): void {
    this.dialogRef.close();
  }

}
