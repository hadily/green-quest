import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
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

  getAllPartners(): Observable<any> {
    const url = `${this.apiUrl}/partner/`;
    return this.http.get<any[]>(url);
  }

  getAllClients(): Observable<any> {
    const url = `${this.apiUrl}/client/`;
    return this.http.get<any[]>(url);
  }

  getAllAdmins(): Observable<any> {
    const url = `${this.apiUrl}/admin/`;
    return this.http.get<any[]>(url);
  }

  createPartner(partner: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/partner/`, partner);
  }

  deletePartner(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/partner/${id}`);
  }

  updatePartner(id: number, partner: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/partner/${id}`, partner);
  }

  getPartnerById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/partner/${id}`);
  }

  searchPartners(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/partner/search`, { params });
  }

  searchClients(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/client/search`, { params });
  }

  createClient(client: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/client/`, client);
  }

  getClientById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/client/${id}`);
  }

  updateClient(id: number, client: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/client/${id}`, client);
  }

  deleteClient(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/client/${id}`);
  }

  searchAdmins(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/admin/search`, { params });
  }

  createAdmin(admin: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin/`, admin);
  }

  getAdminById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/admin/${id}`);
  }

  updateAdmin(id: number, admin: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/admin/${id}`, admin);
  }

  deleteAdmin(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/admin/${id}`);
  }
}
