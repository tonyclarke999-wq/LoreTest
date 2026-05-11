using System.Security.Claims;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.Options;
using LoreTest.Data;

namespace LoreTest.Data
{
    public class AdditionalUserClaimsPrincipalFactory(
            UserManager<ApplicationUser> userManager,
            IOptions<IdentityOptions> optionsAccessor)
            : UserClaimsPrincipalFactory<ApplicationUser>(userManager, optionsAccessor)
    {

        protected override async Task<ClaimsIdentity> GenerateClaimsAsync(ApplicationUser user)
        {
            var identity = await base.GenerateClaimsAsync(user);
            if (!string.IsNullOrEmpty(user.Role))
            {
                identity.AddClaim(new Claim(ClaimTypes.Role, user.Role));
            }
            return identity;
        }
    }
}
