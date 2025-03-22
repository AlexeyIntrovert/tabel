export interface UserProfile {
  email: string;
  fullName: string;
  tabNum?: number;
  position?: string;
  grade?: number;
  group?: { id: number; name: string; code: string; };
  productionType?: { id: number; name: string; };
}
