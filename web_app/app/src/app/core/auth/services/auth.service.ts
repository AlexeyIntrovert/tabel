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
  private isAuthenticatedSubject = new BehaviorSubject<boolean>(false);
  isAuthenticated$ = this.isAuthenticatedSubject.asObservable();
  private userSubject = new BehaviorSubject<User | null>(null);
  user$ = this.userSubject.asObservable();

  constructor(
    private http: HttpClient,
    private router: Router
  ) {
    const token = this.getToken();
    this.isAuthenticatedSubject.next(!!token);
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
      error: () => {
        this.handleUnauthenticated();
      }
    });
  }

  signin(credentials: { email: string; password: string }): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/signin`, credentials).pipe(
      tap(response => {
        this.setToken(response.token);
        this.userSubject.next(response.user);
        this.isAuthenticatedSubject.next(true);
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
    this.removeToken();
    this.userSubject.next(null);
    this.isAuthenticatedSubject.next(false);
    this.router.navigate(['/signin']);
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

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  private removeToken(): void {
    localStorage.removeItem(this.tokenKey);
  }

  private handleError(error: HttpErrorResponse) {
    return throwError(() => error);
  }

  private handleUnauthenticated(): void {
    this.removeToken();
    this.isAuthenticatedSubject.next(false);
    if (!window.location.pathname.includes('/signin')) {
      this.router.navigate(['/signin']);
    }
  }

}
