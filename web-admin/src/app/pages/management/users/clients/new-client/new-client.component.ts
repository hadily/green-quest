import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { HttpClient } from '@angular/common/http';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { tap } from 'rxjs/operators';

@Component({
  selector: 'app-new-client',
  templateUrl: './new-client.component.html',
  styleUrl: './new-client.component.scss'
})
export class NewClientComponent implements OnInit {
  client = {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    phoneNumber: '',
    localisation: '',
    adminId: null,
    roles: ['CLIENT']
  };
  admins : any[] = [];
  file: any;

  constructor(
    public dialogRef: MatDialogRef<NewClientComponent>,
    private http: HttpClient,
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
    this.loadAdmins();
  }

  loadAdmins(): void {
    this.apiService.getAllAdmins().subscribe(
      data => this.admins = data,
      error => console.error('Error fetching admins:', error)
    );
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

  generateRandomPassword(): string {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let password = '';
    for (let i = 0; i < 10; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
  }
  
  onSubmit(): void {
    this.client['password'] = this.generateRandomPassword();
    this.apiService.createClient(this.client, this.file).subscribe(
      response => {
        console.log('Client created:', response);
        this.refreshService.triggerRefresh('/users/clients'); // Emit a value to notify other components
        this.closeModal();
      },
      error => {
        console.error('Error creating partner:', error);
        // Optionally show an error message to the user
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close();
  }

}
