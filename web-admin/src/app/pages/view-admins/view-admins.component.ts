import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/api.service';
import { ChangeDetectorRef } from '@angular/core';

@Component({
  selector: 'app-view-admins',
  templateUrl: './view-admins.component.html',
  styleUrl: './view-admins.component.scss'
})
export class ViewAdminsComponent implements OnInit {
  admins: any[] = [];

  constructor(private apiService: ApiService, private cdr: ChangeDetectorRef) { }  // Use camelCase for service

  ngOnInit(): void {
    this.apiService.getAllAdmins().subscribe(
      data => {
        this.admins = data;
        this.cdr.detectChanges();  // Manually trigger change detection
        console.log(this.admins);
      },
      error => {
        console.log("Error fetching data:", error);
      }
    );
  }

}
