import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = 'http://localhost:8000/api'

  constructor(private http: HttpClient) {}

  getAllUsers(): Observable<any> {
    const url = `${this.apiUrl}/user/`; // Full URL
    return this.http.get(url).pipe(
      tap(data => console.log('Fetched all users:', data)) // Log the response
    );
  }
}
