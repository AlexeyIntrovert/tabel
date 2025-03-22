import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { BehaviorSubject, Observable, throwError, of } from 'rxjs';
import { tap, catchError, finalize, map } from 'rxjs/operators';
import { Router } from '@angular/router';

export interface AuthResponse {
  token: string;
  user: {
    email: string;
    roles: string[];
  };
}

export interface User {
  email: string;
  roles: string[];
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost/api/auth';
  private readonly tokenKey = 'auth_token';
  private readonly userKey = 'user_data';
  private isAuthenticatedSubject = new BehaviorSubject<boolean>(false);
  private userSubject = new BehaviorSubject<User | null>(null);
  
  isAuthenticated$ = this.isAuthenticatedSubject.asObservable();
  user$ = this.userSubject.asObservable();

  constructor(
    private http: HttpClient,
    private router: Router
  ) {
    this.loadStoredData();
  }

  private loadStoredData(): void {
    const token = this.getToken();
    const userData = this.getStoredUser();
    
    if (token && userData) {
      this.isAuthenticatedSubject.next(true);
      this.userSubject.next(userData);
    }
  }

  checkAuth(): void {
    const token = this.getToken();
    if (!token) {
      this.handleUnauthenticated();
      return;
    }

    this.verifyToken().subscribe({
      next: (isAuthenticated) => {
        if (!isAuthenticated) {
          this.handleUnauthenticated();
        }
      },
      error: () => this.handleUnauthenticated()
    });
  }

  signin(credentials: { email: string; password: string }): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/signin`, credentials).pipe(
      tap(response => {
        this.setToken(response.token);
        this.setUser(response.user);
        this.isAuthenticatedSubject.next(true);
        this.userSubject.next(response.user);
      }),
      catchError(this.handleError)
    );
  }

  signup(userData: { email: string; password: string; username: string }): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/signup`, userData).pipe(
      tap(response => {
        this.setToken(response.token);
        this.isAuthenticatedSubject.next(true);
      }),
      catchError(this.handleError)
    );
  }

  signout(): void {
    this.handleUnauthenticated();
  }

  refreshToken(): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/refresh`, {}).pipe(
      tap(response => {
        this.setToken(response.token);
      }),
      catchError(this.handleError)
    );
  }

  verifyToken(): Observable<boolean> {
    return this.http.get<{authenticated: boolean}>(`${this.apiUrl}/check`, {}).pipe(
      map(response => response.authenticated),
      tap(isAuthenticated => {
        this.isAuthenticatedSubject.next(isAuthenticated);
      }),
      catchError(error => {
        this.handleUnauthenticated();
        return of(false);
      })
    );
  }

  private setToken(token: string): void {
    localStorage.setItem(this.tokenKey, token);
  }

  public getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  private removeToken(): void {
    localStorage.removeItem(this.tokenKey);
  }

  private getStoredUser(): User | null {
    const userData = localStorage.getItem(this.userKey);
    return userData ? JSON.parse(userData) : null;
  }

  private setUser(user: User): void {
    localStorage.setItem(this.userKey, JSON.stringify(user));
  }

  private removeUser(): void {
    localStorage.removeItem(this.userKey);
  }

  hasRole(role: string): boolean {
    const user = this.userSubject.value;
    return user?.roles?.includes(role) || false;
  }

  private handleError(error: HttpErrorResponse) {
    return throwError(() => error);
  }

  private handleUnauthenticated(): void {
    this.removeToken();
    this.removeUser();
    this.isAuthenticatedSubject.next(false);
    this.userSubject.next(null);
    if (!window.location.pathname.includes('/signin')) {
      this.router.navigate(['/signin']);
    }
  }

}
