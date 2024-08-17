import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-new-event',
  templateUrl: './new-event.component.html',
  styleUrl: './new-event.component.scss'
})
export class NewEventComponent implements OnInit {
  event = {
    serviceName: '',
    description: '',
    startDate: '',
    endDate: '',
    price: 0,
    available: true
  };
  file: any;

  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private refreshService: RefreshService,
    private router: Router
  ) {}


  ngOnInit(): void {}

  selectImage(event: any) {
    this.file = event.target.files[0];
    let reader = new FileReader();
    reader.onload = function () {
      let output: any = document.getElementById('imageFilename');
      output.src = reader.result;
    }
    reader.readAsDataURL(this.file);
  }


  onSubmit(): void {
    const userId = this.authService.currentUserValue?.id ?? 0;
    console.log(userId);
    this.apiService.createEvent(userId, this.event, this.file).subscribe(response => {
      console.log('Event created:', response);
      this.router.navigate(['/partner/services/events']); 
    },
    error => {
      console.error('Error creating article:', error);
      // Optionally show an error message to the user
    });
  }

  

}
