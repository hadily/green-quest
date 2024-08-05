import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/api.service';
import { ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-view-clients',
  templateUrl: './view-clients.component.html',
  styleUrl: './view-clients.component.scss'
})
export class ViewClientsComponent implements OnInit{
  clients: any[] = [];

  constructor(private apiService: ApiService, private cdr: ChangeDetectorRef) { }  // Use camelCase for service

  ngOnInit(): void {
    this.apiService.getAllClients().subscribe(
      data => {
        this.clients = data;
        this.cdr.detectChanges();  // Manually trigger change detection
        console.log(this.clients);
      },
      error => {
        console.log("Error fetching data:", error);
      }
    );
  }

}
