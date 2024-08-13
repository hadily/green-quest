import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = 'http://localhost:8000/api'

  constructor(private http: HttpClient) {}

  /** GET */
  
  getAllUsers(): Observable<any> {
    const url = `${this.apiUrl}/user/all`; // Full URL
    return this.http.get(url).pipe(
      tap(data => console.log('Fetched all users:', data)) // Log the response
    );
  }

  getUserById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/user/${id}`);
  }

  getAllPartners(): Observable<any> {
    const url = `${this.apiUrl}/partner/`;
    return this.http.get<any[]>(url);
  }

  getPartnerById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/partner/${id}`);
  }

  getAllClients(): Observable<any> {
    const url = `${this.apiUrl}/client/`;
    return this.http.get<any[]>(url);
  }

  getClientById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/client/${id}`);
  }

  getAllAdmins(): Observable<any> {
    const url = `${this.apiUrl}/admin/`;
    return this.http.get<any[]>(url);
  }

  getAdminById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/admin/${id}`);
  }

  getAllArticles(): Observable<any> {
    const url = `${this.apiUrl}/article/`;
    return this.http.get<any[]>(url);
  }

  getArticleById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/article/${id}`);
  }

  getAllComplaints(): Observable<any> {
    const url = `${this.apiUrl}/complaints/`;
    return this.http.get<any[]>(url);
  }

  getClientComplaints(): Observable<any> {
    const url = `${this.apiUrl}/complaints/client-complaints/`;
    return this.http.get<any[]>(url);
  }

  getPartnerComplaints(): Observable<any> {
    const url = `${this.apiUrl}/complaints/partner-complaints/`;
    return this.http.get<any[]>(url);
  }

  getComplaintById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/complaints/${id}`);
  }

  getUsers(): Observable<any> {
    const token = localStorage.getItem('token');
    console.log('token: ', token);
    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);

    return this.http.get(`${this.apiUrl}/user/`, { headers });
  }

  /** CREATE */

  createPartner(partner: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/partner/`, partner);
  }

  
  createClient(client: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/client/`, client);
  }

  createAdmin(admin: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin/`, admin);
  }

  createArticle(article: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/article/`, article);
  }

  /** UPDATE */

  updatePartner(id: number, partner: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/partner/${id}`, partner);
  }

  updateClient(id: number, client: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/client/${id}`, client);
  }

  updateAdmin(id: number, admin: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/admin/${id}`, admin);
  }

  updateArticle(id: number, article: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/article/${id}`, article);
  }

  updateComplaints(id: number, complaint: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/complaints/${id}`, complaint);
  }

  updateUser(id: number, complaint: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/user/${id}`, complaint);
  }

  /** DELETE */

  deletePartner(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/partner/${id}`);
  }

  deleteClient(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/client/${id}`);
  }

  deleteAdmin(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/admin/${id}`);
  }

  deleteArticle(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/article/${id}`);
  }


  /** SEARCH */

  searchPartners(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/partner/search`, { params });
  }

  searchClients(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/client/search`, { params });
  }

  searchAdmins(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/admin/search`, { params });
  }

  searchArticles(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/article/search`, { params });
  }

  
  
}
