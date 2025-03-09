import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { map, take } from 'rxjs/operators';

export const authGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  return authService.isAuthenticated$.pipe(
    take(1),
    map(isAuthenticated => {
      if (!isAuthenticated) {
        // Store the attempted URL for redirecting
        const currentUrl = state.url;
        router.navigate(['/signin'], { 
          queryParams: { returnUrl: currentUrl }
        });
        return false;
      }
      return true;
    })
  );
};
