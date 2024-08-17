import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-delete-product',
  templateUrl: './delete-product.component.html',
  styleUrl: './delete-product.component.scss'
})
export class DeleteProductComponent implements OnInit{

  constructor(
    public dialogRef: MatDialogRef<DeleteProductComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { id: number },
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {}

  onDelete(): void {
    this.apiService.deleteProduct(this.data.id).subscribe(
      response => {
        console.log('product deleted:', response);
        this.dialogRef.close(true); 
        this.refreshService.triggerRefresh('/partner/services/products'); // Navigate to the partner list page

      },
      error => {
        console.error('Error deleting event:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); 
  }


}
