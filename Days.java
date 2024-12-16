public class Days
{
    public static void main (String ar[])
    {
    int day,week,year,n;
      n=730;
     year= n/365;
     week=n/52;
     day=week/7;
     System.out.println("years are = " + year );
     System.out.println("weeks are = " + week );
     System.out.println("days are = " + day );
    }
}