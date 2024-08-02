import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})
export class ApiService {
  public apiBaseUrl = 'http://localhost:8000/api'; // Update with your Symfony API URL

  constructor(private http: HttpClient) {}

  getAllUsers(): Observable<any> {
    const url = `${this.apiBaseUrl}/user/`;
    return this.http.get(url).pipe(
      tap(data => console.log('Fetched all users:', data)) // Log the response
    );
  }
}
