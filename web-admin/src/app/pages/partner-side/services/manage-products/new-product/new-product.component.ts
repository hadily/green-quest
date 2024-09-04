import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
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
    imageFilename: null,
  };

  constructor(
    public dialogRef: MatDialogRef<NewProductComponent>,
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService,
  ) {}

  ngOnInit(): void {}

  selectImage(event: any) {
    this.product.imageFilename = event.target.files[0]; 
  }

  onSubmit(): void {
    this.product.owner = this.authService.currentUserValue?.id ?? 1;

    this.apiService.createProduct(this.product).subscribe(response => {
      console.log('Event created:', response);
      this.closeModal();
        this.refreshService.triggerRefresh('/partner/blog/articles');
    },
    error => {
      console.error('Error creating article:', error);
    });
  }

  closeModal(): void {
    this.dialogRef.close();
  }

}
