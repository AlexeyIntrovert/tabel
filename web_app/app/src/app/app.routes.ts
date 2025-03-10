import { Routes } from '@angular/router';
import { authGuard } from './core/auth/guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    pathMatch: 'full',
    redirectTo: 'calendar'
  },
  {
    path: 'signin',
    loadComponent: () => import('./auth/signin/signin.component')
      .then(m => m.SigninComponent)
  },
  {
    path: 'signup',
    loadComponent: () => import('./auth/signup/signup.component')
      .then(m => m.SignupComponent)
  },
  {
    path: 'calendar',
    canActivate: [authGuard],
    loadComponent: () => import('./calendar/calendar.component')
      .then(m => m.CalendarComponent)
  },
  {
    path: 'settings',
    canActivate: [authGuard],
    loadComponent: () => import('./settings/settings.component')
      .then(m => m.SettingsComponent)
  },
  {
    path: 'projects',
    canActivate: [authGuard],
    loadComponent: () => import('./projects/projects.component')
      .then(m => m.ProjectsComponent)
  },
  {
    path: '**',
    redirectTo: ''
  }
];
