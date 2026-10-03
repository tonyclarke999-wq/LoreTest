using System;
using Microsoft.AspNetCore.Identity;

class Program
{
    static void Main()
    {
        var hasher = new PasswordHasher<string>();
        var hash = hasher.HashPassword("dummy", "Password1-");
        Console.WriteLine("HASH:" + hash);
    }
}
