import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { map, switchMap } from 'rxjs/operators';
import { of } from 'rxjs';

export const authGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  return authService.verifyToken().pipe(
    switchMap(isAuthenticated => {
      if (!isAuthenticated) {
        router.navigate(['/signin'], {
          queryParams: { returnUrl: state.url }
        });
        return of(false);
      }

      // Check roles for projects route
      if (state.url === '/projects') {
        const hasAccess = authService.hasRole('ROLE_MANAGER') || 
                         authService.hasRole('ROLE_HEADER');
        
        if (!hasAccess) {
          router.navigate(['/settings']);
          return of(false);
        }
      }

      return of(true);
    })
  );
};
