export interface Calendar {
  date: string;
  type: 'W' | 'P' | 'H' | 'R';
  hours: number;
}

export interface Month {
  id: number;
  name: string;
  days: Calendar[];
}
