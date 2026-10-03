using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace LoreTest.Tests
{
    [TestClass]
    public class ImportTests
    {
        [TestMethod]
        public void PlaceholderTest()
        {
            var result = int.Parse("1") + 1;
            Assert.AreEqual(2, result);
        }
    }
}
