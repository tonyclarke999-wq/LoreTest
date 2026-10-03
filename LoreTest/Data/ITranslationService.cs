using System.Threading.Tasks;

namespace LoreTest.Data
{
    public interface ITranslationService
    {
        Task<string> TranslateAsync(string text, string fromCulture, string toCulture);
    }
}
